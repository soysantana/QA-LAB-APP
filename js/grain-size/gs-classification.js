function clasificarSuelo(gravel, sand, fines, Cu, Cc, LL = null, IP = null) {
    let code = "";
    let description = "";

    if (fines < 50 && gravel > sand && fines < 5 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW";
        description = "Well Graded Gravel";
    } else if (fines < 50 && gravel > sand && fines < 5 && Cu >= 4 && Cc >= 0.5 && Cc <= 3 && sand >= 15) {
        code = "GW";
        description = "Well Graded Gravel with sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW-GM";
        description = "Well Graded Gravel with silt";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GM";
        description = "Well Graded Gravel with silt and sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand < 15) {
        code = "GW-GC";
        description = "Well Graded Gravel with clay";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GC";
        description = "Well Graded Gravel with silty clay";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GC";
        description = "Well Graded Gravel with clay and sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu >= 4 && Cc >= 1 && Cc <= 3 && sand >= 15) {
        code = "GW-GC";
        description = "Well Graded Gravel with silty clay and sand";
    } else if (fines < 50 && gravel > sand && fines < 5 && (Cu < 4 || Cc < 1 || Cc > 3) && sand < 15) {
        code = "GP";
        description = "Poorly Graded Gravel";
    } else if (fines < 50 && gravel > sand && fines < 5 && (Cu < 4 || Cc < 1 || Cc > 3) && sand >= 15) {
        code = "GP";
        description = "Poorly Graded Gravel with sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        code = "GP-GM";
        description = "Poorly Graded Gravel with silt";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && (Cu < 4 || Cc < 1 || Cc > 3) && sand >= 15) {
        code = "GP-GM";
        description = "Poorly Graded Gravel with silt and sand";
    } else if (fines < 50 && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand < 15) {
        code = "GP-GC";
        description = "Poorly Graded Gravel with clay";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines <= 12 && Cu < 4 && Cc < 1 && Cc > 3 && sand >= 15) {
        code = "GP-GC";
        description = "Poorly Graded Gravel with clay and sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GM";
        description = "Silty Gravel";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GM";
        description = "Silty Gravel with sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GC";
        description = "Clayey Gravel";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GC";
        description = "Clayey Gravel with sand";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand < 15) {
        code = "GC-GM";
        description = "Silty Clayey Gravel";
    } else if (fines < 50 && gravel > sand && fines >= 5 && fines > 12 && sand >= 15) {
        code = "GC-GM";
        description = "Silty Clayey Gravel with sand";
    } else if (fines < 50 && sand > gravel && fines < 5 && Cu >= 6 && Cc >= 0.5 && Cc <= 3 && gravel < 15) {
        code = "SW";
        description = "Well Graded Sand";
    } else if (fines < 50 && sand > gravel && fines < 5 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW";
        description = "Well Graded Sand with gravel";
    } else if (fines < 50 && sand > gravel && fines < 5 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel < 15) {
        code = "SP";
        description = "Poorly Graded Sand";
    } else if (fines < 50 && sand > gravel && fines < 5 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel >= 15) {
        code = "SP";
        description = "Poorly Graded Sand with gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        code = "SW-SM";
        description = "Well Graded Sand with silt";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW-SM";
        description = "Well Graded Sand with silt and gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel < 15) {
        code = "SW-SC";
        description = "Well Graded Sand with clay";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu >= 6 && Cc >= 1 && Cc <= 3 && gravel >= 15) {
        code = "SW-SC";
        description = "Well Graded Sand with clay and gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && (Cu < 6 || Cc < 1 || Cc > 3) && gravel < 15) {
        code = "SP-SM";
        description = "Poorly Graded Sand with silt";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel >= 15) {
        code = "SP-SM";
        description = "Poorly Graded Sand with silt and sand";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu > 6 && Cc >= 1 && Cc <= 3.4 && gravel >= 15) {
        code = "SP-SM";
        description = "Poorly Graded Sand with silt and gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel < 15) {
        code = "SP-SC";
        description = "Poorly Graded Sand with clay";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines <= 12 && Cu < 6 && Cc < 1 && Cc > 3 && gravel >= 15) {
        code = "SP-SC";
        description = "Poorly Graded Sand with clay and gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SM";
        description = "Silty Sand";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SM";
        description = "Silty Sand with gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SC";
        description = "Clayey Sand";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SC";
        description = "Clayey Sand with gravel";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel < 15) {
        code = "SC-SM";
        description = "Silty Clayey Sand";
    } else if (fines < 50 && sand > gravel && fines >= 5 && fines > 12 && gravel >= 15) {
        code = "SC-SM";
        description = "Silty Clayey Sand with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) < 30 && (100 - fines) < 15) {
        code = "CL";
        description = "Lean Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29 && sand >= gravel)) {
        code = "CL";
        description = "Lean Clay with sand";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29 && sand < gravel)) {
        code = "CL";
        description = "Lean Clay with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) >= 30 && sand >= gravel && gravel < 15) {
        code = "CL";
        description = "Sandy Lean Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) >= 30 && sand >= gravel && gravel >= 15) {
        code = "CL";
        description = "Sandy Lean Clay with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) >= 30 && sand < gravel && sand < 15) {
        code = "CL";
        description = "Gravely Lean Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP > 7) && (100 - fines) >= 30 && sand < gravel && sand >= 15) {
        code = "CL";
        description = "Gravely Lean Clay with sand";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) < 30 && (100 - fines) < 15) {
        code = "CL-ML";
        description = "Silty Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand >= gravel) {
        code = "CL-ML";
        description = "Silty Clay with sand";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand < gravel) {
        code = "CL-ML";
        description = "Silty Clay with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) >= 30 && sand >= gravel) {
        code = "CL-ML";
        description = "Sandy Silty Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) >= 30 && sand >= gravel && gravel < 15) {
        code = "CL-ML";
        description = "Sandy Silty Clay with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) >= 30 && sand < gravel && sand < 5) {
        code = "CL-ML";
        description = "Gravely Silty Clay";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) <= IP) && IP <= 7 && IP >= 4) && (100 - fines) >= 30 && sand < gravel && sand < 15) {
        code = "CL-ML";
        description = "Gravely Silty Clay with sand";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) < 30 && (100 - fines) < 15) {
        code = "ML";
        description = "Silt";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand >= gravel) {
        code = "ML";
        description = "Silt with sand";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand < gravel) {
        code = "ML";
        description = "Silt with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) >= 30 && sand >= gravel && gravel < 15) {
        code = "ML";
        description = "Sandy Silt";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) >= 30 && sand >= gravel && gravel >= 15) {
        code = "ML";
        description = "Sandy Silt with gravel";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) >= 30 && sand < gravel && sand < 15) {
        code = "ML";
        description = "Gravely Silt";
    } else if (fines >= 50 && LL < 50 && ((0.73 * (LL - 20) > IP) || IP < 4) && (100 - fines) >= 30 && sand < gravel && sand >= 15) {
        code = "ML";
        description = "Gravely Silt with sand";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) < 30 && (100 - fines) < 15) {
        code = "CH";
        description = "Fat Clay";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) < 29.4) && sand >= gravel) {
        code = "CH";
        description = "Fat Clay with sand";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) < 29.4) && sand < gravel) {
        code = "CH";
        description = "Fat Clay with gravel";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) >= 30 && sand >= gravel && gravel < 15) {
        code = "CH";
        description = "Sandy Fat Clay";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) >= 30 && sand >= gravel && gravel >= 15) {
        code = "CH";
        description = "Sandy Fat Clay with gravel";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) >= 30 && sand < gravel && sand < 15) {
        code = "CH";
        description = "Gravelly Fat Clay";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) <= IP) && (100 - fines) >= 30 && sand < gravel && sand >= 15) {
        code = "CH";
        description = "Gravelly Fat Clay with sand";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) < 30 && (100 - fines) < 15) {
        code = "MH";
        description = "Elastic Silt";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand >= gravel) {
        code = "MH";
        description = "Elastic Silt with sand";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) < 30 && ((100 - fines) >= 15 && (100 - fines) <= 29) && sand < gravel) {
        code = "MH";
        description = "Elastic Silt with gravel";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) >= 30 && sand >= gravel && gravel < 15) {
        code = "MH";
        description = "Sandy Elastic Silt";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) >= 30 && sand >= gravel && gravel >= 15) {
        code = "MH";
        description = "Sandy Elastic Silt with gravel";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) >= 30 && sand < gravel && sand < 15) {
        code = "MH";
        description = "Gravelly Elastic Silt";
    } else if (fines >= 50 && LL >= 50 && (0.73 * (LL - 20) > IP) && (100 - fines) >= 30 && sand < gravel && sand >= 15) {
        code = "MH";
        description = "Gravelly Elastic Silt with sand";
    } else {
        code = "NA";
        description = "No se pudo clasificar el suelo.";
    }

    return { code, description };
}

function clasificarSueloExtra(WtRetExtendidaArray) {
    const extraInfo =
        (WtRetExtendidaArray[0] !== 0 && WtRetExtendidaArray[4] !== 0 && WtRetExtendidaArray[9] !== 0)
            ? "with Boulders and Cobbles"
            : (WtRetExtendidaArray[0] !== 0)
                ? "Boulders"
                : (WtRetExtendidaArray[4] !== 0 || WtRetExtendidaArray[9] !== 0)
                    ? "with Cobbles"
                    : "";

    return extraInfo;
}

export { clasificarSuelo, clasificarSueloExtra };